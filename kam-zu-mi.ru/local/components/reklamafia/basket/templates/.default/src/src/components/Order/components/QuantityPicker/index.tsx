import * as React from "react";
import { useState } from "react";
import styled from "styled-components";

const Container = styled.div`
  display: flex;

  input.value {
    border-radius: 100px;
    border: 1px solid #aaa;
    color: #272727;
    cursor: default;
    flex: 0 0 auto;
    font-weight: 500;
    padding: 15px 3px;
    padding: 6px 3px;
    text-align: center;
    width: 50px;
  }

  button {
    background-color: transparent;
    border-radius: 50%;
    border: none;
    color: #848484;
    cursor: pointer;
    display: block;
    flex: 0 0 auto;
    font-size: 2em;
    font-weight: 500;
    line-height: 1.4444444444;
    outline: 0;
    padding: 5px 26px;
    position: relative;
    width: auto;
  }
`;

export default function QuantityPicker({
  value,
  onChange,
}: {
  value: number;
  onChange: any;
}) {
  const handleChange = (newValue: number) => {
    let valueToSet = newValue;
    if (valueToSet < 1) valueToSet = 1;
    if (valueToSet > 20) valueToSet = 20;
    onChange(valueToSet);
  };

  return (
    <Container>
      <button
        onClick={(e) => {
          e.preventDefault();
          handleChange(value - 1);
        }}
      >
        -
      </button>
      <input className="value" type="text" readOnly value={value} />
      <button
        onClick={(e) => {
          e.preventDefault();
          handleChange(value + 1);
        }}
      >
        +
      </button>
    </Container>
  );
}
