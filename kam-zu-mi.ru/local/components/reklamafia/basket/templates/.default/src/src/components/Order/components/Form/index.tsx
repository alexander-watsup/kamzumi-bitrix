import * as React from "react";
import styled from "styled-components";

import { useContext } from "react";
import { observer } from "mobx-react-lite";
import { StoreContext } from "../../../../helpers/store";

import CourierForm from "./Courier";
import PickupForm from "./Pickup";

const Container = styled.div``;

const Form = observer(() => {
  const store = useContext(StoreContext);

  /*
    {deliveryMethod === "courier" && <CourierForm cities={cities}/>}
        {deliveryMethod === "pickup" && <PickupForm pickupPoints={pickupPoints}/>}
      */
  return (
    <Container>
      {store.deliveryMethod === "courier" && <CourierForm />}
      {store.deliveryMethod === "pickup" && <PickupForm />}
    </Container>
  );
});
export default Form;
