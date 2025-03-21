<template>
<!--    <Spinner v-show="this.loading"/>-->
<!--    <Success :message="message"/>-->

{{order}}

</template>
<script>
// import Spinner from "../../instruments/Spinner.vue";
// import Success from "../../instruments/Success.vue";
import Pusher from 'pusher-js'


export default {
    components: {
        // Success,
        // Spinner,
    },
    data: function () {
        return {
            order: '',
            errors: '',
            message: null,
            loading: false,
            pusher: null,  // Для хранения объекта Pusher
            channel: null, // Для хранения канала
        }
    },
    mounted() {
        this.checkMessages()
    },
    beforeDestroy() {
        // Отписываемся от канала и уничтожаем соединение Pusher при удалении компонента
        if (this.channel) {
            this.channel.unbind_all();
            this.pusher.unsubscribe('check_amount');
        }
    },
    methods: {
        checkMessages() {
            const pusher = new Pusher('6c99314bac482dfe845e', {
                cluster: 'eu', logToConsole: true,
            })

            // pusher.connection.bind('state_change', function(states) {
            //     console.log('Pusher connection state changed: ', states);
            // });
            //
            // pusher.connection.bind('error', function(error) {
            //     console.error('Pusher connection error: ', error);
            // });

            const channel = pusher.subscribe('check_amount')

            channel.bind('my-event', (data) => {
                console.log(data)
                this.order = data.order
                // this.updateOrderComment(data.orderId, data.comment)

            })
        },
    },
}
</script>
<style>

</style>

