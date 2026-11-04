<?php

namespace App\Http\Controllers;

use App\Achievement;
use App\CertificateDesign;
use App\Curriculum;
use App\Organization;
use App\Services\CertificateDesigner\DesignValidator;
use App\User;
use Illuminate\Http\Request;

class CertificateDesignerController extends Controller
{
    private function json($data, int $status = 200)
    {
        return response()->json($data, $status)->header('Cache-Control', 'private, no-store');
    }

    private function scoped()
    {
        return CertificateDesign::where('owner_id', auth()->id())
            ->where('organization_id', auth()->user()->current_organization_id);
    }

    public function index()
    {
        //        $this->authorize('certificate_access');

        $organization = Organization::findOrFail(auth()->user()->current_organization_id);

        return response()->view('certificate-designer.index', ['config' => [
            'apiBase'      => url('/certificate_designer/api'),
            'csrf'         => csrf_token(),
            'organization' => ['name' => $organization->title, 'city' => $organization->city ?? ''],
        ]])->header('Cache-Control', 'private, no-store');
    }

    public function templates()
    {
        return $this->json($this->scoped()->latest()->get(['id', 'title', 'curriculum_id', 'revision', 'updated_at']));
    }

    public function show(int $id)
    {
        $design = $this->scoped()->findOrFail($id);
        abort_unless(Curriculum::findOrFail($design->curriculum_id)->isAccessible(), 403);

        return $this->json($design);
    }

    public function store(Request $request, DesignValidator $validator)
    {
        // certificate_access permits personal designs, never edits to shared legacy templates.
        $this->authorize('certificate_access');

        $values = $this->validated($request, $validator);
        $design = CertificateDesign::create($values + [
            'owner_id'        => auth()->id(),
            'organization_id' => auth()->user()->current_organization_id,
            'revision'        => 1,
        ]);

        return $this->json(['id' => $design->id, 'revision' => $design->revision], 201);
    }

    public function update(Request $request, DesignValidator $validator, int $id)
    {
        $this->authorize('certificate_access');

        $this->scoped()->findOrFail($id);
        $request->validate(['revision' => 'required|integer|min:1']);
        $values             = $this->validated($request, $validator);
        $values['document'] = json_encode($values['document'], JSON_THROW_ON_ERROR);
        // Compare-and-swap prevents silent overwrite by another browser tab.
        $updated = $this->scoped()->where('id', $id)->where('revision', $request->integer('revision'))
            ->update($values + ['revision' => $request->integer('revision') + 1]);
        abort_unless($updated === 1, 409, 'Die Vorlage wurde zwischenzeitlich geändert. Exportiere deinen Entwurf und lade die gespeicherte Version neu.');

        return $this->json(['id' => $id, 'revision' => $request->integer('revision') + 1]);
    }

    public function destroy(int $id)
    {
        $this->authorize('certificate_access');

        $this->scoped()->findOrFail($id)->delete();

        return response()->noContent();
    }

    private function validated(Request $request, DesignValidator $validator): array
    {
        abort_unless($request->isJson(), 415, 'Bitte die Vorlage als JSON senden.');
        $input = $request->validate([
            'title' => 'required|string|max:180', 'curriculum_id' => 'required|integer', 'document' => 'required|array',
        ]);
        $curriculum = Curriculum::findOrFail($input['curriculum_id']);
        abort_unless($curriculum->isAccessible(), 403);
        $ids = $curriculum->terminalObjectives()->with('enablingObjectives')->get()
            ->flatMap(fn ($area) => $area->enablingObjectives->pluck('id'))->all();
        // Preserve intentional whitespace and empty strings in user-authored layouts.
        $raw                        = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $input['document']          = $raw['document'];
        $input['document']['title'] = $input['title'];
        $input['document']          = $validator->validate($input['document'], $ids);

        return $input;
    }

    public function curricula(Request $request)
    {
        $request->validate(['q' => 'nullable|string|max:150']);
        $user  = auth()->user();
        $query = Curriculum::query()->where('title', 'like', '%' . ($request->input('q') ?? '') . '%');
        if (! is_admin()) {
            $query->where(function ($q) use ($user) {
                $q->where('type_id', 1)->orWhere('owner_id', $user->id)
                    ->orWhereHas('userSubscriptions', fn ($s) => $s->where('subscribable_id', $user->id))
                    ->orWhereHas('groupSubscriptions', fn ($s) => $s->whereIn('subscribable_id', $user->groups->pluck('id')))
                    ->orWhereHas('organizationSubscriptions', fn ($s) => $s->where('subscribable_id', $user->current_organization_id));
            });
        }

        return $this->json($query->orderBy('title')->limit(30)->get()->map(fn ($c) => ['id' => $c->id, 'title' => $this->plain($c->title)]));
    }

    public function curriculum(Curriculum $curriculum)
    {
        abort_unless($curriculum->isAccessible(), 403);
        $areas = $curriculum->terminalObjectives()->with('enablingObjectives')->get();

        return $this->json([
            'curriculum' => ['id' => $curriculum->id, 'title' => $this->plain($curriculum->title)],
            'areas'      => $areas->map(fn ($a) => [
                'id'         => (string) $a->id, 'title' => $this->plain($a->title),
                'objectives' => $a->enablingObjectives->map(fn ($o) => ['id' => (string) $o->id, 'title' => $this->plain($o->title)]),
            ]),
        ]);
    }

    public function students(Request $request)
    {
        $request->validate(['q' => 'nullable|string|max:150']);
        $q = $request->input('q') ?? '';
        if (mb_strlen($q) < 2) {
            return $this->json([]);
        }
        // Match existing mayAccessUser organization boundary; never expose other schools.
        $users = User::whereHas('organizations', fn ($org) => $org->where('organizations.id', auth()->user()->current_organization_id))
            ->where(function ($query) use ($q) {
                $query->where('firstname', 'like', '%' . $q . '%')->orWhere('lastname', 'like', '%' . $q . '%');
            })->orderBy('lastname')->limit(30)->get(['users.id', 'firstname', 'lastname']);

        return $this->json($users->filter(fn ($u) => auth()->user()->mayAccessUser($u))
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->fullName()])->values());
    }

    public function achievements(Curriculum $curriculum, User $student)
    {
        abort_unless($curriculum->isAccessible() && auth()->user()->mayAccessUser($student), 403);
        $ids = $curriculum->terminalObjectives()->with('enablingObjectives')->get()
            ->flatMap(fn ($a) => $a->enablingObjectives->pluck('id'));
        $values = Achievement::where('referenceable_type', 'App\\EnablingObjective')
            ->whereIn('referenceable_id', $ids)->where('user_id', $student->id)
            ->orderBy('updated_at')->orderBy('id')->get(['referenceable_id', 'status'])->pluck('status', 'referenceable_id');

        return $this->json(['student' => ['id' => $student->id, 'name' => $student->fullName()], 'achievements' => (object) $values->all()]);
    }

    private function plain(?string $html): string
    {
        return trim(html_entity_decode(strip_tags(preg_replace('/<br\s*\/?\s*>/i', ' ', $html ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
