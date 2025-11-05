## 1.2.2 (2025-10-17)
- KanbanItem: allow Comments by guests
- ObjectiveMedia: added ConfirmModal for deletion
- TerminalObjectives: fixed 'delete-objective' not sending delete-request
Videoconference: fixed issue where users accessing through token-links didn't have access
- MediumPreviewModal: highlighted close-button to make it more visible
- MapMarker: fixed MediumSubscriptions not updating when viewing another Marker
- AchievementsApiController: created 'get'-endpoint, overhauled 'store'-endpoint and added documentation

## 1.2.1 (2025-10-06)
- Videoconference: fixed abort-condition and messages
- MapMarker: allow delete by admins
- MapMarker: fixed logic for showing edit/delete icon
- MediumPreviewModal: further improvements/overhaul and style changes
- MediaCarousel: now opens MediumPreviewModal when clicking on image
- MediaCarousel: moved 'create-links' overlay to MediumPreviewModal
- added option to load Edusharing-media in lower/higher quality
- Kanban: now loads background image in highest resolution

## 1.2.0 (2025-09-25)
- preparations for WebSockets
- updated axios to fix vulnerability
- changed translation from KanbanStatus to "Spalte"
- KanbanItem: added functionality to add (multiple) media on create
- Kanban: added missing attributes on copyKanban/copyStatus/copyItem
- Kanban: hide buttons from MediaCarousel and Comments on print
- Kanban: Comments can now be deleted by Kanban-owner
- Comments: always show delete-icon on smaller devices
- tinyMCE: fixed media-button problems
- Map: fixed created Marker not having the same type/category (thus not appearing after reload)
- LogbookEntryModal: removed media-plugin from tinyMCE and added lists
- rework of MediumPreviewModal
- MediumPreviewModal: show media in original quality instead of preview
- EdusharingMedia: added Kanban/KanbanStatus as always visible
- Authenticate: implemented logic to force SSO on token-links
- KanbanAPI: fixed delete-endpoint, validation/responses and documentation
- MediaCarousel: added loading-overlay on delete

## 1.1.1 (2025-08-27)
- LinkItem: fixed QR-Codes not rendering sometimes
- Group: fixed adding a course not refering to the correct course-ID
- RenderUsage: fixed positioning of image in Carousel
- fixed Media not opening on-clik in Safari (event not trusted)
- CertificateModal: added code-plugin
- GenerateCertificateModal: fixed listing unavailable certificates

## 1.1.0 (2025-08-21)
- KanbanItemModal/KanbanStatusModal: fixed permissions not showing on create for non-kanban-owners
- fixed authenticated users not being able to create/update/delete KanbanItem/-Status when accessing through token-link
- Kanban: most icons are now hidden on print-view
- Logbook/Map: removed import of unused 'MediumPreviewModal'
- Objective: fixed content-tab ID not being unique
- removed unused component 'KanbanTask'
- updated vite to latest (requiring: node >=22.12.0)
- updated tinyMCE to v8 (latest)
- removed 'curriculummedia' as a plugin from tinyMCE
- removed unused packages:
  - `@activix/bootstrap-datetimepicker` and `vue3-datepicker`
  - `@vue/cli-service` and `@vue/compat`
  - `cypress-vite`
  - `dompurify` and `vue-dompurify-html`
  - `laravel-mix`

## 1.0.4 (2025-08-15)
- IndexWidget: added accessibility logic
- Groups: added scope for groups with default period_id
- changed modal-header structure
- changed dropdown-toggle animation
- deleted unused/deprecated ColorPicker-component
- fixed header and sidebar overlay problems
- fixed AchievementIndicators being visible in occasions where they shouldn't
- fixed badge-counter styling
- fixed objectives-styling for tablet/mobile view
- ObjectiveBox-Header: fixed arrow positioning and accessibility
- Objectives: wip on changing layout for mobile-view
- Plan/PlanAchievements: fixed buttons styling/structure and accessibility
- AchievementIndicator: show last-updated date in local date format
- LinkItem: fixed links overflowing and special-chars being parsed to HTML

## 1.0.3 (2025-08-08)
- Kanban: fixed 'collapse-all'-toggle not working
- KanbanItem: removed isAccessible-check on /editors-request (which was causing a 403 for guests)
- fixed media not always being accessible on KanbanItems
- EdusharingMediaAdapter: fixed redirect paramter
- RenderUsage: now sends /content-request for Edusharing-media on click
- RenderUsage: fixed overlay position
- fixed topnav dropdown-menu positioning

## 1.0.2 (2025-08-06)
- Home: admins now only get their subscribed logbooks instead of all
- added 'skip navigation'-button in Header for accessibility
- restructured scss-files
- Kanban: fixed accessibility for title-component
- Kanban: fixed not being able to remove description
- KanbanItem: fixed comments/reactions structure and added accessibility
- KanbanItem: fixed sending /editors-request if user is not given
- Map: fixed problem when no marker exists
- MapModal: fixed problem with type_id/category_id fields and added toast-notification
- MarkerModal: fixed some logic and form-field-types in controller
- added seeder for MapMarkerCategory/-Type

## 1.0.1 (2025-07-23)
- Kanban: fixed bug where authenticated users didn't have access through a token-link
- KanbanItem: fixed visible-from/-to logic
- Exams: fixed exams not loading
- Users: now ordering by lastname, then firstname
- TerminalObjective: fixed error on changing type order
- Token: reset title-field on create
- Curriculum: fixed reset_order_ids function

## 1.0.0 (2025-07-16)
- fixed tinyMCE-problems when setting up project
- CertificateController: copied missing function from old version
- KanbanModal: added toast-notifications