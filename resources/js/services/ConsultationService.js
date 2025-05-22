import axios from "axios";

export class ConsultationService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getMessages(query = '', page = 1) {
        const url = `${this.serverUrl}/consultation/messages?query=${encodeURIComponent(query)}&page=${page}`;
        return axios.get(url);
    }
    static getTodayMessages(message_id) {
        let url = `${this.serverUrl}/consultation/today_messages/${message_id}`;
        return axios.get(url, message_id);
    }
    static sendConsultantMessages(messageId, message) {
        let url = `${this.serverUrl}/consultation/send_message/${messageId}`
        return axios.post(url, { message })
    }
    static setConsultantMessagesRead(messageId) {
        let url = `${this.serverUrl}/consultation/set_read_messages/${messageId}`
        return axios.patch(url, messageId)
    }
}
