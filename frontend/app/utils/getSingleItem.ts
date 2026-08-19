const singleItem = async (apiEndpoint: string, itemId: number) => {
  let response = await useApiFetch(apiEndpoint + "/" + itemId);
  return response;
};

export default { singleItem };
