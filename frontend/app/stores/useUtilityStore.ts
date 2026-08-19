import { defineStore } from "pinia";

export const useUtilityStore = defineStore({
  id: "useUtilityStore",
  state: () => ({
    dataList: [],
    pageTitle: "",
    pageBackLink: "",
    isVmodelData: false,
    formData: {},
    formDataArr: [],
    isLoading: false,
    is_edit: false,
    toastMsg: "",
    pending: "",
    apiEndpoint: "",
    isCreateMore: false,
  }),

  getters: {
    getToastMsg: (state) => state.toastMsg,
  },
});
