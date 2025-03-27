import axios from "axios";

export class UserService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static currentUser() {
        let url = `${this.serverUrl}/users/me`
        return axios.get(url)
    }
    static getUsers()
    {
        let url = `${this.serverUrl}/users`;
        return axios.get(url)
    }
    static getManagers()
    {
        let url = `${this.serverUrl}/users/managers`;
        return axios.get(url)
    }
    // static getUserStatistic()
    // {
    //     let url = `${this.serverUrl}/user/statistics`;
    //     return axios.get(url)
    // }
    // static createSubAccount(sub_account) {
    //     let url = `${this.serverUrl}/user/sub_account/create`;
    //     return axios.post(url, sub_account)
    // }
    // static updateSubAccount(update_sub_account)
    // {
    //     let url = `${this.serverUrl}/user/sub_account/${update_sub_account.id}`;
    //     return axios.patch(url, update_sub_account)
    // }


}
