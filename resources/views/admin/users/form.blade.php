@csrf
@include ('forms.input.text', 
            ["model" => "user", 
            "field" => "username", 
            "placeholder" => "Username",  
            "required" => true, 
            "value" => old('username', isset($user) ? $user->username : '')])

@include ('forms.input.text', 
            ["model" => "user", 
            "field" => "firstname", 
            "placeholder" => "Joe",  
            "required" => true, 
            "value" => old('firstname', isset($user) ? $user->firstname : '')])

@include ('forms.input.text', 
            ["model" => "user", 
            "field" => "lastname", 
            "placeholder" => "Diet",  
            "required" => true, 
            "value" => old('lastname', isset($user) ? $user->lastname : '')])

@include ('forms.input.text', 
            ["model" => "user", 
            "field" => "email", 
            "placeholder" => "joe.diet@curriculumonline.de",  
            "required" => true, 
            "value" => old('email', isset($user) ? $user->email : '')])

@include ('forms.input.password', 
            ["model" => "user", 
            "field" => "password", 
            "placeholder" => "Top_Secret!",  
            "required" => true, 
            "value" => ''])

                                         

<div>
    <input class="btn btn-info" type="submit" value="{{ $buttonText }}">
</div>