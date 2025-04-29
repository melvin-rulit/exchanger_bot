export function handleApiError(error, defaultMessage = 'Произошла ошибка') {
    const response = error?.response?.data;

    return {
        success: response?.success ?? false,
        message: response?.error || response?.message || response?.errors?.[0] || defaultMessage,
    };
}

// export function handleApiError(error, defaultMessage = 'Произошла ошибка') {
//     return error?.response?.data?.success||
//         error?.response?.data?.error ||
//         error?.response?.data?.message ||
//         error?.response?.data?.errors?.[0] ||
//         defaultMessage;
// }
