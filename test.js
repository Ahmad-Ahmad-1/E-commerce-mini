const cancelOrder = async (orderId) => {
    try {
        const response = await axios.put(/api/orders / ${ orderId } / cancel);
        return response.data;
    } catch (error) {
        console.error("Failed to cancel order", error);
        throw error;
    }
};