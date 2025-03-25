import axios from 'axios'

export class OrdersService {
    static serverUrl = import.meta.env.VITE_BASE_URL

    static getOrders(query) {
        let url = `${this.serverUrl}/orders?query=` + query
        return axios.get(url)
    }

    static getOrder(id) {
        let url = `${this.serverUrl}/orders/get_order/${id}`
        return axios.patch(url, id)
    }

    static setOrderMessagesRead(id) {
        let url = `${this.serverUrl}/orders/set_read_messages/${id}`
        return axios.patch(url, id)
    }

    static sendOrderMessages(orderId, message) {
        let url = `${this.serverUrl}/orders/send_message/${orderId}`
        return axios.post(url, { message })
    }
    static store(form) {
        let url = `${this.serverUrl}/orders/update_order`;
        return axios.put(url, form)
    }
    static close_order(form) {
        let url = `${this.serverUrl}/orders/close_order`;
        return axios.put(url, form)
    }
    static fix_order(orderId) {
        let url = `${this.serverUrl}/orders/fix_order/${orderId}`;
        return axios.put(url)
    }
}
