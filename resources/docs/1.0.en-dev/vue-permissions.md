# Check permissions in vue

---

- [can directive](#section-can)
- [hide-if-permission directive](#section-hide)
- [permission directive](#section-permission)

! Always check permissions in the backand.
These directives enable shorter syntax on vue.

<a name="section-can"></a>
## can directive

Custom Vue directive "can" to check against permissions. If permission is not given element gets style display:none

Example:
```html
<element v-can="'curriculum_edit'"><element>
```

<a name="section-hide"></a>
## hide-if-permission directive

Custom Vue directive "hide-if-permission" to check against permissions. If permission is given element gets style display:none


Example:
```html
<element v-hide-if-permission="'curriculum_edit'"><element>
```

<a name="section-permission"></a>
## permission directive

Custom Vue directive "permission" to check against permissions. 
If permission is not given element gets removed from DOM.


Example:
```html
<element v-permission="'curriculum_edit'"><element>
```
