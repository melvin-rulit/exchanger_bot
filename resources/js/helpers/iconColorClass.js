export function getIconColorClass(type) {
    switch (type) {
        case 'success':
            return 'icon-success';
        case 'error':
            return 'icon-error';
        case 'danger':
            return 'icon-danger';
        case 'info':
            return 'icon-info';
        default:
            return 'icon-success';
    }
}
