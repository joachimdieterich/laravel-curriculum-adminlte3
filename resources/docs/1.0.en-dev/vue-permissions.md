# Check permissions in vue

---

- [Can directive](#section-1)



<a name="section-1"></a>
## Can directive

Custom Vue directive "can" to check against permissions. If permission is not given element gets style display:none

! Always check permissions in the backand. 
This directive enables shorter syntax on vue.

Example:
```html
<element v-can="'curriculum_edit'" ><element>
```