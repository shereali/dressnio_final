let formValidation = (state: object) => {
  let errors: any = [];
  Object.entries(state).forEach(([key, value]) => {
    console.log("key", key);
    if (value.length == 0) {
      errors.push({ path: key, message: `*${key}*` + " field is required" });
    }
  });

  return errors;
};

export default formValidation;
