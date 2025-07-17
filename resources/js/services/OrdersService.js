import axios from 'axios'

export class OrdersService {
    static serverUrl = import.meta.env.VITE_BASE_URL

    static getOrders(query = '', page = 1) {
        const url = `${this.serverUrl}/orders?query=${encodeURIComponent(query)}&page=${page}`;
        return axios.get(url);
    }
    static getAllOrders(query = '', page = 1) {
        const url = `${this.serverUrl}/orders/all_orders?query=${encodeURIComponent(query)}&page=${page}`;
        return axios.get(url);
    }
    static getOrdersWithElasticSearch(query = '', page = 1) {
        const url = `${this.serverUrl}/orders/elastic_search?query=${encodeURIComponent(query)}&page=${page}`;
        return axios.get(url);
    }
    static getOrdersWithSearch(params = {}, page = 1) {
        return axios.get(`${this.serverUrl}/orders/search`, {
            params: {
                ...params,
                page,
            },
        });
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
    static sendOrderMessagesWithImage(orderId, photoFile, caption) {
        let url = `${this.serverUrl}/orders/send_photo/${orderId}`

        let formData = new FormData()
        formData.append('photo', photoFile)
        formData.append('caption', caption)

        return axios.post(url, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
    }
    static assignExecutor(form) {
        let url = `${this.serverUrl}/orders/assign_executor`;
        return axios.put(url, form)
    }
    static updateStatus(orderId, status) {
        let url = `${this.serverUrl}/orders/status/${orderId}`
        return axios.patch(url, {status})
    }
    static end_order(form) {
        let url = `${this.serverUrl}/orders/end_order`;
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
