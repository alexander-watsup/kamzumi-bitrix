import * as React from "react";

import { useContext } from "react";
import { observer } from "mobx-react-lite";
import { StoreContext } from "../../../../helpers/store";

import styled from "styled-components";

const Container = styled.div`
  background-color: #f8ca4d;  
  padding: 30px;
  text-align: center;
  font-size: 1.5em;
  @media (max-width: 330px) {
    font-size: 1em;
  }
`;

const Info = observer(({ text }: { text: string }) => {
  const store = useContext(StoreContext);
  if (!text) return <></>;
  return <Container>{text}</Container>;
});

export default Info;
