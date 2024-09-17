# TinyMCE

---

- [Usage](#section-1)


Curriculum uses [vue-toastification](https://www.npmjs.com/package/vue-toastification)
<a name="section-1"></a>

## Usage
```js 

this.toast("Default toast");
this.toast.info("Info toast");
this.toast.success("Success toast");
this.toast.error("Error toast");
this.toast.warning("Warning toast");

//Full config
this.toast.success(
    message, 
    {
        position: "top-right",
        timeout: 3000,
        closeOnClick: true,
        pauseOnFocusLoss: true,
        pauseOnHover: true,
        draggable: true,
        draggablePercent: 0.6,
        showCloseButtonOnHover: false,
        hideProgressBar: false,
        closeButton: "button",
        icon: true,
        rtl: false
    }
);
```
