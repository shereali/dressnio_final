export default function (errors: any, form?: any) {
  const errorsArray: Array<any> = [];
  if (errors && typeof errors === "object") {
    Object.entries(errors).forEach(([key, value]) => {
      if (Array.isArray(value) && value.length > 0) {
        errorsArray.push({ path: key, message: value[0] });
      } else if (typeof value === "string") {
        errorsArray.push({ path: key, message: value });
      }
    });
  }

  if (errorsArray.length > 0 && form) {
    if (typeof form.setErrors === "function") {
      form.setErrors(errorsArray);
    } else if (form.value && typeof form.value.setErrors === "function") {
      form.value.setErrors(errorsArray);
    }
  }
}
