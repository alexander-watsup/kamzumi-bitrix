import { makeAutoObservable, runInAction, autorun } from "mobx";
import { createContext, useContext } from "react";
import { IAppProps, IDeliveryInfo } from "../interfaces";
import { hashCode } from "./helpers";
import { sendTelegramMessage } from "./telegram";

const deliveryDefaultValue: IDeliveryInfo = {
  name: "",
  phone: "",
  comment: "",
  guestCount: 0,
  pickup: null,
  courier: null,
};

export class Store {
  initValues: IAppProps;
  deliveryMethod: string;

  delivery: IDeliveryInfo | null;
  deliveryInfo: {
    code: number;
    message: string;
    price: number;
    requestHash: string;
  } | null;
  fetchState: {
    delivery: string;
    order: string;
  };
  paymentMethod: string;
  diagUserFormData: string;
  diagUserPhone: string;

  constructor(initValues: IAppProps) {
    this.initValues = initValues;
    this.deliveryMethod = "courier";
    this.delivery = deliveryDefaultValue;
    this.deliveryInfo = null;
    this.fetchState = {
      delivery: "",
      order: "",
    };
    this.paymentMethod = "onDeliveryCash";
    this.diagUserFormData = "";
    this.diagUserPhone = "";

    makeAutoObservable(this);

    autorun(() => this.updateDeliveryInfo(), {
      delay: 3000,
    });
  }

  get price() {
    let price = 0;
    this.initValues.basket.map((item: any) => {
      price += item.price * item.quantity;
    });
    return price;
  }

  get priceWithDelivery() {
    let price = 0;
    this.initValues.basket.map((item: any) => {
      price += item.price * item.quantity;
    });
    if (this.deliveryInfo?.price > 0 && this.deliveryMethod === "courier") {
      price += this.deliveryInfo.price;
    }
    return price;
  }

  get isCourierDelivery() {
    return this.deliveryMethod === "courier";
  }

  get isPickupDelivery() {
    return this.deliveryMethod === "pickup";
  }

  get canMakeOrder() {
    let result = false;

    if (
      this.deliveryMethod === "courier" &&
      this.delivery?.courier &&
      this.deliveryInfo?.code === 0 &&
      this.fetchState.delivery == ""
    ) {
      result = true;
    }

    if (this.deliveryMethod === "pickup" && this.delivery?.pickup) {
      result = true;
    }

    return result;
  }

  setPaymentMethod(method: string) {
    this.paymentMethod = method;
  }

  setDeliveryMethod(method: string) {
    this.deliveryMethod = method;
  }

  prepareCourierDelivery(props: IDeliveryInfo | null) {
    if (!props) {
      this.delivery = null;
      return;
    }

    this.delivery = deliveryDefaultValue;
    this.delivery.name = props.name || "";
    this.delivery.phone = props.phone || "";
    this.delivery.comment = props.comment || "";
    this.delivery.guestCount = props.guestCount || 0;
    this.delivery.courier = {
      ...(props.courier || {
        city: "",
        street: "",
        house: "",
        housing: "",
        apartment: "",
      }),
    };
    this.delivery.pickup = null;
  }

  preparePickupDelivery(props: IDeliveryInfo | null) {
    if (!props) {
      this.delivery = null;
      return;
    }

    this.delivery = deliveryDefaultValue;
    this.delivery.name = props.name || "";
    this.delivery.phone = props.phone || "";
    this.delivery.comment = props.comment || "";
    this.delivery.guestCount = props.guestCount || 0;
    this.delivery.pickup = {
      pickupPointId: props.pickup.pickupPointId,
    };
    this.delivery.courier = null;
  }

  async createOrder() {
    if (this.delivery.courier) {
      this.fetchState.order = "creating";

      let courierOrderParams = {
        deliveryType: "courier",
        deliveryPrice: this.deliveryInfo.price,
        paymentType: this.paymentMethod,

        name: this.delivery.name,
        phone: this.delivery.phone,
        guestCount: this.delivery.guestCount,
        comment: this.delivery.comment,

        city: this.delivery.courier.city,
        street: this.delivery.courier.street,
        house: this.delivery.courier.house,
        housing: this.delivery.courier.housing,
        apartment: this.delivery.courier.apartment,
      };
      let url = "https://kam-zu-mi.ru/api/v1/orders/createOrder";
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(courierOrderParams),
      });
      const responseJson = await response.json();

      if (responseJson.error === 0 && responseJson.redirectUrl) {
        window.location.href = responseJson.redirectUrl;
      }
      this.fetchState.order = "";
    }

    if (this.delivery.pickup) {
      this.fetchState.order = "creating";

      let courierOrderParams = {
        deliveryType: "pickup",
        paymentType: this.paymentMethod,

        name: this.delivery.name,
        phone: this.delivery.phone,
        guestCount: this.delivery.guestCount,
        comment: this.delivery.comment,

        pickupPointId: this.delivery.pickup.pickupPointId,
      };

      let url = "https://kam-zu-mi.ru/api/v1/orders/createOrder";
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(courierOrderParams),
      });
      const responseJson = await response.json();

      if (responseJson.error === 0 && responseJson.redirectUrl) {
        window.location.href = responseJson.redirectUrl;
      }
      this.fetchState.order = "";
    }
  }

  async updateDeliveryInfo() {
    if (!this.delivery) {
      this.deliveryInfo = null;
      return;
    }

    if (!this.delivery.courier && !this.delivery.pickup) {
      this.deliveryInfo = null;
      return;
    }

    if (this.fetchState.delivery == "fetching") return;

    if (this.delivery.courier) {
      let courierOrderParams = {
        date: "",
        order: {
          items: this.initValues.basket.map((item) => {
            return { id: item.id, amount: item.quantity };
          }),
          phone: this.delivery.phone,
          customerName: this.delivery.name,
          address: {
            city: this.delivery.courier.city,
            street: this.delivery.courier.street,
            home: this.delivery.courier.house,
          },
        },
      };

      const hc = hashCode(JSON.stringify(courierOrderParams.order.address));

      if (hc === this.deliveryInfo?.requestHash) {
        //console.log("skip", hc);
        return;
      } else {
        //console.log("fetch");
        this.fetchState.delivery = "fetching";
        let url = "https://kam-zu-mi.ru/api/v1/iiko/delivery/checkOrder";
        const response = await fetch(url, {
          method: "POST",
          cache: "no-cache",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(courierOrderParams), // body data type must match "Content-Type" header
        });
        const responseJson = await response.json();
        this.deliveryInfo = {
          code: responseJson.data.code,
          message: responseJson.data.message,
          price: responseJson.data.deliveryPrice,
          requestHash: hc,
        };
        this.fetchState.delivery = "";

        // Диагностическое сообщение в Телеграм в случае ошибки
        if (responseJson.data.code !== 0) {
          const responseCodes = {
            "0": "OK",
            "1": ", отказано по сумме заказа.",
            "2": ", отказано по времени работы.",
            "3": ", нет подходящей зоны.",
            "4": ", продукт из заказа в стоп-листе.",
            "5": ", продукт из заказа запрещён к продаже.",
          };
          let dm = "";
          if (responseJson.data.code in responseCodes) {
            dm = responseCodes[responseJson.data.code];
          }
          sendTelegramMessage(
            "IIKO не разрешает доставку" +
              dm +
              "\r\nКлиент: " +
              courierOrderParams.order.customerName +
              " " +
              courierOrderParams.order.phone +
              "\r\nАдрес: " +
              courierOrderParams.order.address.city +
              " " +
              courierOrderParams.order.address.street +
              " " +
              courierOrderParams.order.address.home
          );
        }
      }
    }

    if (this.delivery.pickup) {
      this.deliveryInfo = null;
    }
  }
}

export const StoreContext = createContext(null);
