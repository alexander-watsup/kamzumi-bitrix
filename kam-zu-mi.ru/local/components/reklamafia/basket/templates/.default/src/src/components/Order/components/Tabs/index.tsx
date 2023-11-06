import * as React from "react";

import { useContext } from "react";
import { observer } from "mobx-react-lite";
import { StoreContext } from "../../../../helpers/store";

import styled from "styled-components";

const Container = styled.div`
  display: flex;
  justify-content: center;
  font-size: 3em;
  &>div {
    padding: 0px 40px;
  }
  @media (max-width: 600px) {
    font-size: 2em;
    &>div {
      padding: 0px 10px;
    }
  }
  @media (max-width: 330px) {
    font-size: 1.5em;
    &>div {
      padding: 0px 10px;
    }
  }
`;

const TabsItem = styled.div<{ readonly selected: boolean }>`
  cursor: ${(props: any) => (props.selected ? "default" : "pointer")};

  color: ${(props: any) => (props.selected ? "black" : "#444")};
  border-bottom: ${(props: any) =>
    props.selected ? "3px solid #f8ca4d" : "3px solid white"};
`;

const Tabs = observer(() => {
  const store = useContext(StoreContext);

  return (
    <Container>
      <TabsItem
        selected={store.deliveryMethod === "courier"}
        onClick={() => store.setDeliveryMethod("courier")}
      >
        Доставка
      </TabsItem>
      <TabsItem
        selected={store.deliveryMethod === "pickup"}
        onClick={() => store.setDeliveryMethod("pickup")}
      >
        Самовывоз
      </TabsItem>
    </Container>
  );
});

export default Tabs;
