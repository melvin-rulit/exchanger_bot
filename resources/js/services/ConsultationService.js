import axios from "axios";

export class ConsultationService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getMessages() {
        let url = `${this.serverUrl}/consultation/messages`;
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
}
