import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

let toastMessage = (message: string, msgType: string) => {
  if (msgType == "success")
    toast.success(message, {
      autoClose: 5000,
    });
  if (msgType == "warning")
    toast.warn(message, {
      autoClose: 5000,
    });
  if (msgType == "info")
    toast.info(message, {
      autoClose: 5000,
    });
  if (msgType == "error")
    toast.error(message, {
      autoClose: 5000,
    });
};

export default toastMessage;
