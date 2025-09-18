const getOrders = useCallback(async (page = 1, append = false) => {
    try {
        const response = await axios.get(/api/orders ? page = ${ page }, {
            withCredentials: true,
            headers: { Accept: "application/json" },
        });
        const newOrders = response.data.data;
        setAllOrders((prevOrders) =>
            append ? [...prevOrders, ...newOrders] : newOrders
        );
        setCurrentAllOrdersPage(response.data.meta.current_page);
        setLastAllOrdersPage(response.data.meta.last_page);
    } catch (err) {
        console.error("Failed to load orders", err);
    }
}, []);

const loadMoreOrders = () => {
    if (currentAllOrdersPage < lastAllOrdersPage) {
        getOrders(currentAllOrdersPage + 1, true);
    }
};