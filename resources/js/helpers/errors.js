export function handleApiError(error, defaultMessage = 'Произошла ошибка') {
    const response = error?.response?.data;

    return {
        success: response?.success ?? false,
        message: response?.error || response?.message || response?.errors?.[0] || defaultMessage,
    };
}
