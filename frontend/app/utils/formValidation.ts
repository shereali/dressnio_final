let formValidation = (state: any) => {
  let errors: any = [];
  if (!state || typeof state !== "object") return errors;

  Object.entries(state).forEach(([key, value]: [string, any]) => {
    if (
      value === null ||
      value === undefined ||
      (typeof value === "string" && value.trim().length === 0) ||
      (Array.isArray(value) && value.length === 0)
    ) {
      errors.push({ path: key, message: `*${key}* field is required` });
    }
  });

  return errors;
};

export default formValidation;
