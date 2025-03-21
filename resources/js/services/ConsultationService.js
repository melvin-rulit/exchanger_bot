import axios from "axios";

export class ConsultationService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getMessages() {
        let url = `${this.serverUrl}/consultation/get_messages`;
        return axios.get(url);
    }
    // static getSubAccount(value) {
    //     let url = `${this.serverUrl}/user/sub_account/${value.id}`;
    //     return axios.get(url, value);
    // }
    // static store(form) {
    //     let url = `${this.serverUrl}/user/sub_account/create`;
    //     return axios.post(url, form)
    // }
    // static update(sub_account) {
    //     let url = `${this.serverUrl}/user/sub_account/${sub_account.id}`;
    //     return axios.patch(url, sub_account)
    // }
    // static delete(value) {
    //     let url = `${this.serverUrl}/user/sub_account/${value.id}`;
    //     return axios.delete(url, value)
    // }
    //
    // static getExchanges() {
    //     let url = `${this.serverUrl}/exchanges`;
    //     return axios.get(url);
    // }

}
