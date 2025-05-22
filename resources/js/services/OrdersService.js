import axios from 'axios'

export class OrdersService {
    static serverUrl = import.meta.env.VITE_BASE_URL

    static getOrders(query = '', page = 1) {
        const url = `${this.serverUrl}/orders?query=${encodeURIComponent(query)}&page=${page}`;
        return axios.get(url);
    }
    static getOrder(orderId) {
        let url = `${this.serverUrl}/orders/get_order/${orderId}`
        return axios.get(url, orderId)
    }
    static setOrderMessagesRead(orderId) {
        let url = `${this.serverUrl}/orders/set_read_messages/${orderId}`
        return axios.patch(url, orderId)
    }
    static sendOrderMessages(orderId, message, isRequisite) {
        let url = `${this.serverUrl}/orders/send_message/${orderId}`
        return axios.post(url, { message, isRequisite})
    }
    static assignExecutor(form) {
        let url = `${this.serverUrl}/orders/assign_executor`;
        return axios.put(url, form)
    }
    static updateStatus(orderId, status) {
        let url = `${this.serverUrl}/orders/status/${orderId}`
        return axios.patch(url, {status})
    }
    static close_order(form) {
        let url = `${this.serverUrl}/orders/close_order`;
        return axios.put(url, form)
    }
    static fix_order(orderId) {
        let url = `${this.serverUrl}/orders/fix_order/${orderId}`;
        return axios.put(url)
    }
    static updateClientName(orderId, first_name) {
        let url = `${this.serverUrl}/orders/update_client_name/${orderId}`
        return axios.patch(url, { first_name })
    }
}
