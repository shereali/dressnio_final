export default function (errors: Array<any>, form: Object) {
  const errorsArray: Array<any> = [];
  if (errors && typeof errors === "object") {
    Object.entries(errors).forEach(([key, value]) => {
      console.log(key);
      // Ensure 'value' is an array and has at least one element
      if (Array.isArray(value) && value.length > 0) {
        errorsArray.push({ path: key, message: value[0] });
      }
    });
  }

  if (errorsArray.length > 0) {
    form?.value.setErrors(errorsArray);
  }
}
