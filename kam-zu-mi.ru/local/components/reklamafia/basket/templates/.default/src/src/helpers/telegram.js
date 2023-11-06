const sendTelegramMessage = async (message) => {

    let reportParams = {
        message,
    };
    let url = "https://kam-zu-mi.ru/api/v1/telegram/send";
    const response = await fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(reportParams),
    });
    const responseJson = await response.json();
}

export { sendTelegramMessage };

