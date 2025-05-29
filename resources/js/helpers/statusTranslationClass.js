export const statusTranslations = {
    new: 'Новый',
    active: 'Активный',
    success: 'Завершен',
    closed: 'Отменен',
    deleted: 'Удален',
}

export function translateStatus(status) {
    return statusTranslations[status] || status
}
