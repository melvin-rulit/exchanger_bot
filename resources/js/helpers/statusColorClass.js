export function getStatusColorClass(status) {
    switch (status) {
        case 'new':
            return 'text-[#38b0b0]'
        case 'active':
            return 'text-[#F93827]'
        case 'success':
            return 'text-[#DBDBDB]'
        case 'closed':
            return 'text-[#DBDBDB]'
        case 'deleted':
            return 'text-[#FF0000]'
        default:
            return 'text-black'
    }
}
