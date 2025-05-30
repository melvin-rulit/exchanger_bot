export function getStatusColorClass(status) {
    switch (status) {
        case 'new':
            return 'text-cyan-600'
        case 'active':
            return 'text-red-500'
        case 'success':
            return 'text-gray-400'
        case 'closed':
            return 'text-gray-400'
        case 'deleted':
            return 'text-red-500'
        default:
            return 'text-black'
    }
}
