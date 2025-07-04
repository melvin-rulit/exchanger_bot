import axios from "axios";

export class ClientService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getClients()
    {
        let url = `${this.serverUrl}/clients`;
        return axios.get(url)
    }
}
