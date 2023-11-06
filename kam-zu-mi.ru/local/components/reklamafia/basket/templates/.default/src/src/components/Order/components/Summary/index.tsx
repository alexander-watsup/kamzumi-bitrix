import * as React from "react";
import { useContext } from "react";

import { observer } from "mobx-react-lite";
import { StoreContext } from "../../../../helpers/store";
import { sendTelegramMessage } from "./../../../../helpers/telegram";

import styled from "styled-components";
import { Container, Row, Col } from "react-awesome-styled-grid";

const Root = styled.div`
  background-color: #eaeaea;
  align-items: center;
  padding: 20px 0;

  .pricing {
    padding: 10px;
    .title {
      font-weight: 600;
    }
    .c50 {
      width: 50%;
      float: left;

      .price {
        font-size: 1.5em;
      }
    }
    .c100 {
      width: 100%;
      clear: left;
      padding-top: 20px;
      .info {
        background-color: #f8ca4d;
        margin-top: 5px;
        padding: 10px;
      }
    }
  }

  .summary {
    padding: 10px;
    text-align: center;
    font-size: 3em;
  }

  .order {
    padding: 10px;
    text-align: center;
    button {
      padding: 20px 30px;
      border: none;
      background-color: #f8ca4d;
      &:disabled {
        background-color: #ddd;
      }
    }
    .report-button {
      color: black;
      text-decoration: underline;
      cursor: pointer;
      margin-top: 10px;
    }
  }
`;

const CourierInfo = observer(() => {
  const store = useContext(StoreContext);

  return (
    <div>
      <div className="title">Доставка</div>
      {store.fetchState.delivery === "fetching" && (
        <div>Рассчитываем доставку...</div>
      )}

      {store.deliveryInfo?.price > 0 &&
        store.fetchState.delivery !== "fetching" && (
          <div className="price">{store.deliveryInfo?.price} р.</div>
        )}

      {store.deliveryInfo &&
        !store.deliveryInfo.price &&
        store.fetchState.delivery !== "fetching" && (
          <div className="price">Бесплатно</div>
        )}

      {store.deliveryInfo?.code === 1 && (
        <div className="price">Проблема :(</div>
      )}
    </div>
  );
});

const PickupInfo = observer(() => {
  const store = useContext(StoreContext);

  return (
    <div>
      <div className="title">Самовывоз</div>
      <div className="price">Бесплатно</div>
    </div>
  );
});

const PricingC = observer(() => {
  const store = useContext(StoreContext);

  return (
    <div className="pricing">
      <div className="c50">
        <div className="title">Заказ</div>
        <div className="price">{store.price} р.</div>
      </div>
      <div className="c50">
        {store.isCourierDelivery && <CourierInfo />}
        {store.isPickupDelivery && <PickupInfo />}
      </div>
      <div className="c100">
        {store.deliveryInfo?.code === 1 && (
          <div className="info">
            Сумма заказа слишком маленькая для доставки в указанный адрес.
            Добавьте ещё товаров в корзину.
          </div>
        )}
        {!store.deliveryInfo &&
          store.deliveryMethod === "courier" &&
          store.fetchState.delivery !== "fetching" && (
            <div className="warning">
              Стоимость доставки не рассчитана. Цена на заказ может измениться.
            </div>
          )}
      </div>
    </div>
  );
});

const SummaryC = observer(() => {
  const store = useContext(StoreContext);

  return (
    <div className="summary">
      <div className="price">{store.priceWithDelivery} р.</div>
    </div>
  );
});

const OrderC = observer(() => {
  const store = useContext(StoreContext);

  const reportProblem = async () => {
    let message =
      "Гость попросил помощи на сайте.\r\n" + store.diagUserFormData;
    sendTelegramMessage(message);
    alert(
      "Мы получили сообщение о проблеме и скоро вам перезвоним по телефону " +
        store.diagUserPhone +
        ". Вы также можете позвонить нам самостоятельно +7 (4012) 31-31-02"
    );
  };

  return (
    <div className="order">
      <button
        disabled={!store.canMakeOrder}
        onClick={() => store.createOrder()}
      >
        Оформить
      </button>
      {!store.canMakeOrder && store.diagUserPhone && (
        <div className="report-button" onClick={reportProblem}>
          Проблемы?
        </div>
      )}
    </div>
  );
});

const Summary = observer(() => {
  const store = useContext(StoreContext);

  return (
    <Root>
      <Container>
        <Row>
          <Col xs={4} sm={8} md={4}>
            <PricingC />
          </Col>
          <Col xs={4} sm={4} md={2}>
            <SummaryC />
          </Col>
          <Col xs={4} sm={4} md={2}>
            <OrderC />
          </Col>
        </Row>
      </Container>
    </Root>
  );
});

export default Summary;
