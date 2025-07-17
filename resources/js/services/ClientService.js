import axios from "axios";

export class ClientService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getClients()
    {
        let url = `${this.serverUrl}/clients`;
        return axios.get(url)
    }

    static updateClientComment(clientId, comment) {
        let url = `${this.serverUrl}/clients/update_comment/${clientId}`
        return axios.patch(url, { comment })
    }

    static updateClientName(orderId, first_name) {
        let url = `${this.serverUrl}/clients/update_client_name/${orderId}`
        return axios.patch(url, { first_name })
    }
}
