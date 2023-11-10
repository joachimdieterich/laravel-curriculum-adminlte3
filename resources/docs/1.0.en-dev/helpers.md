# Generating PDFs (Certificates,...)

---

- [Introduction](#section-1)
- [Available Methods](#section-2)


<a name="section-1"></a>
## Introduction

Curriculum includes some global "helper" PHP functions. 

<a name="section-2"></a>
## Available Methods

### format_select_input($input)
helper function for selects. If input is an array, it returns the first value of array
else it returns the input.



### getImmediateChildrenByTagName(DOMElement $element, $tagName)
Traverse an elements children and collect those nodes that have the tagname specified in $tagName. Non-recursive

### relativeToAbsolutePaths($string)
Convert relative filepaths to absolute paths.

### str_singular()

translate to Str::singular

### starts_with()
translate to Str::startsWith()

### ends_with()
translate to Str::endsWith()

### camel_case()
translate to Str::camel()

### str_limit()
translato to Str::limit()

### str_contains()
translate to Str::contains()

### is_dir_empty($directory)
Check if $directory is empty. 
Returns true if empty, null if $directory not exists.

### find_all_files($directory)
Returns array of files found in $directory.

### now_online()
Returns the number of users who were online now.

### today_online()
Returns the number of users who were online today.

### is_admin()
Returns true if user has admin role.

### is_creator()
Returns true if user has creator role.

### is_schooladmin()
Returns true if user has schooladmin role.

### is_teacher()
Returns true if user has teacher role.

### checkForEmbededMedia($model, $field = 'description')
Searches for media-sources in the description of given model. </br>
Also sets subscription for every media found.

### str_replace_special_chars($string)
Replaces language specific characters with A-Z/a-z equivalent and special characters with an empty string. </br>
Returns string with replaced characters.