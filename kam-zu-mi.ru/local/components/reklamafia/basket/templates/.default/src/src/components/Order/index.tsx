import * as React from "react";

const { DateTime } = require("luxon");

import styled from "styled-components";

import Tabs from "./components/Tabs";
import Info from "./components/Info";
import Form from "./components/Form";
import Payment from "./components/Payment";
import Summary from "./components/Summary";

const Container = styled.div`
  max-width: 900px;
  margin: auto;
  & > div {
    margin-bottom: 30px;
  }
`;

const WorkingHoursInfo = () => {
  const now = DateTime.now();
  const from = now.set({ hour: 11, minutes: 0 });
  const to = now.set({ hour: 22, minutes: 45 });

  const inWorkingHours = from <= now && now <= to;

  return (
    <>
      {!inWorkingHours && (
        <Info text="Кам-Зу-Ми работает ежедневно с 11:00 до 22:45. Сейчас отдыхает." />
      )}
    </>
  );
};

const Order = () => {
  return (
    <Container>
      <Tabs />
      <WorkingHoursInfo />
      <Form />
      <Payment />
      <Summary />
    </Container>
  );
};

export default Order;
