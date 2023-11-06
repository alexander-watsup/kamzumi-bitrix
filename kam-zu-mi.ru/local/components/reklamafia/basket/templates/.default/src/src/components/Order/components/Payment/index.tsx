import * as React from "react";

import { useContext } from "react";
import { observer } from "mobx-react-lite";
import { StoreContext } from "../../../../helpers/store";

import styled from "styled-components";

const Container = styled.div`
  display: flex;
  margin-top: 30px;
`;

const PayMethod = styled.div<{ readonly selected: boolean }>`
  width: 250px;
  border: ${(props: any) =>
    props.selected ? "4px solid #f8ca4d" : "1px solid #ddd"};
  margin: 10px;
  text-align: center;
  padding: ${(props: any) => (props.selected ? "20px" : "23px")};
  cursor: ${(props: any) => (props.selected ? "default" : "pointer")};
  font-weight: ${(props: any) => (props.selected ? "600" : "500")};
  background-color: #fafafa;
`;

const Payment = observer(() => {
  const store = useContext(StoreContext);

  return (
    <Container>
      <PayMethod
        selected={store.paymentMethod === "onDeliveryCash"}
        onClick={() => store.setPaymentMethod("onDeliveryCash")}
      >
        Оплата при получении
      </PayMethod>
      <PayMethod
        selected={store.paymentMethod === "onSiteSB"}
        onClick={() => store.setPaymentMethod("onSiteSB")}
      >
        Оплата картой на сайте
      </PayMethod>
    </Container>
  );
});

export default Payment;
