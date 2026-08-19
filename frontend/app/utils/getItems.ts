const items = async (apiEndpoint: string, page: number = 1) => {
  const response = await useApiFetch(apiEndpoint, {
    query: {
      _page: page,
    },
    watch: [page],
  });

  return response;
};

export default { items };
