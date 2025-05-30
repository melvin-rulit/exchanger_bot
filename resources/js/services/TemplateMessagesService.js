import axios from "axios";

export class TemplateService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getTemplateMessages() {
        let url = `${this.serverUrl}/template`;
        return axios.get(url);
    }
    static storeNewTemplate(template) {
        let url = `${this.serverUrl}/template/message/add`;
        return axios.post(url, { template })
    }
    static updateTemplateMessage(templateId, templateMessage) {
        let url = `${this.serverUrl}/template/message/update/${templateId}`
        return axios.patch(url, { templateMessage })
    }
    static delete(templateId) {
        let url = `${this.serverUrl}/template/message/${templateId}`;
        return axios.delete(url, templateId)
    }
}
