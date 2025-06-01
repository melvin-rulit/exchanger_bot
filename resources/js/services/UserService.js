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
    static updateUser(userId, userForm) {
        let url = `${this.serverUrl}/users/current_user/update/${userId}`
        return axios.patch(url, { userForm })
    }
    static getManagers()
    {
        let url = `${this.serverUrl}/users/managers`;
        return axios.get(url)
    }
    static sendPasswordForUnlock(password) {
        let url = `${this.serverUrl}/users/unlocked/send_password`
        return axios.patch(url, {password})
    }
    static saveIsLock() {
        let url = `${this.serverUrl}/users/locked`
        return axios.patch(url, {})
    }
    static saveIsLockPassword(password) {
        let url = `${this.serverUrl}/users/locked/set_password`
        return axios.patch(url, {password})
    }
    static getPinedChat() {
        let url = `${this.serverUrl}/users/pined/chat`
        return axios.get(url)
    }
    static pinChat(orderId, clientId) {
        let url = `${this.serverUrl}/users/pin/chat`
        return axios.post(url, {orderId, clientId})
    }
    static unPinChat(orderId, chatId) {
        let url = `${this.serverUrl}/users/un_pin/chat`
        return axios.patch(url, {orderId, chatId})
    }
    static toggleNotification(notification) {
        let url = `${this.serverUrl}/users/toggle/notification`
        return axios.patch(url, {notification})
    }
    static sendPhoto(photoFile) {
        let url = `${this.serverUrl}/users/send_photo/`

        let formData = new FormData()
        formData.append('photo', photoFile)

        return axios.post(url, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
    }
}
