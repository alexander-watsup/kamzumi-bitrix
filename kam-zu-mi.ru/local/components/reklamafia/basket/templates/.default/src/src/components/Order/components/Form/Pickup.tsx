import * as React from "react";
import { useState, useEffect, useContext } from "react";

import { IPickupPoint } from "../../../../interfaces";

import { observer } from "mobx-react-lite";
import { StoreContext } from "../../../../helpers/store";

import QuantityPicker from "./../QuantityPicker";
import InputMask from "react-input-mask";

import { titleCase, layoutFix } from "../../../../helpers/helpers";

import styled from "styled-components";
import { Container, Row, Col } from "react-awesome-styled-grid";

const Root = styled.div``;

const FormItem = styled.div`
  width: 100%;
  label {
    display: block;
    font-size: 1.3em;
    font-weight: 400;
    span {
      color: red;
    }
  }
  input,
  select {
    font-size: 2em;
    border: 1px solid #aaa;

    width: 100%;
    height: 50px;

    &:hover {
      border: 1px solid #111;
    }
    &.error {
      border: 2px solid red;
    }
  }

  select {
    background-color: white !important;
  }

  @media (max-width: 330px) {
    input,
    select {
      font-size: 1.5em;
    }
  }
`;

const PickupPoint = styled.div<{ readonly selected: boolean }>`
  width: 100%;
  border: ${(props: any) =>
    props.selected ? "4px solid #f8ca4d" : "1px solid #ddd"};
  text-align: center;
  padding: ${(props: any) => (props.selected ? "20px" : "23px")};
  cursor: ${(props: any) => (props.selected ? "default" : "pointer")};
  font-weight: ${(props: any) => (props.selected ? "600" : "500")};
  background-color: #fafafa;
`;

const validate = (values: any) => {
  let result: any = {};

  if (values.name.length == 0) result.name = "";
  if (values.name.length == 1) result.name = " ";

  if (values.phone.valueFiltered.length == 0) result.phone = "";
  if (
    values.phone.valueFiltered.length > 1 &&
    values.phone.valueFiltered.length !== 11
  )
    result.phone = " ";

  return result;
};

const Pickup = observer(() => {
  const store = useContext(StoreContext);
  const [values, setValues] = useState({
    name: "",
    phone: {
      value: "",
      valueFiltered: "",
    },
    guestCount: 1,
    comment: "",
    pickupPointId: store.initValues.pickupPoints[0].id,
  });

  const errors = validate(values);
  const isValid = Object.keys(errors).length === 0;

  // Подготовка диагностического сообщения для Телеграмм
  useEffect(() => {
    let data =
      values.name +
      " " +
      values.phone.value +
      "\r\n" +
      (values.guestCount ? " гостей:" + values.guestCount : "") +
      (values.comment ? " коммент." + values.comment : "");

    data += "\r\n";
    store.initValues.basket.map((item) => {
      data += " " + item.name + " " + item.quantity + "шт.";
    });
    store.diagUserFormData = data;
    store.diagUserPhone = values.phone.value;
  }, [values]);

  useEffect(() => {
    if (!isValid) {
      store.preparePickupDelivery(null);
    } else {
      store.preparePickupDelivery({
        name: values.name,
        phone: values.phone.valueFiltered,
        comment: values.comment,
        guestCount: values.guestCount,
        pickup: {
          pickupPointId: values.pickupPointId,
        },
      });
    }
  }, [values]);

  return (
    <Root>
      <Container>
        <Row style={{ marginBottom: "30px" }}>
          <Col xs={4} sm={4}>
            <FormItem>
              <label>
                Имя: <span>*</span>
              </label>
              <input
                type="text"
                placeholder=""
                value={values.name}
                onChange={(e) => {
                  let newName = layoutFix(e.target.value).replace(
                    /[^А-Яа-яЁё\s]/g,
                    ""
                  );
                  newName = titleCase(newName);
                  setValues({ ...values, name: newName });
                }}
              />
            </FormItem>
          </Col>
          <Col xs={4} sm={4}>
            <FormItem>
              <label>
                Телефон: <span>*</span>
              </label>
              <InputMask
                mask="\+\7 999 999 99 99"
                name="phone"
                size={20}
                className={errors.phone ? "error" : ""}
                value={values.phone.value}
                onChange={(e) => {
                  let valueFiltered = e.target.value.replace(/[^\d]/g, "");
                  setValues({
                    ...values,
                    phone: {
                      value: e.target.value,
                      valueFiltered,
                    },
                  });
                }}
              />
            </FormItem>
          </Col>
        </Row>

        <Row style={{ marginBottom: "30px" }}>
          {(store.initValues.pickupPoints || []).map((point: IPickupPoint) => {
            return (
              <Col xs={4} sm={4} md={2} lg={4} xl={4} key={point.id}>
                <PickupPoint
                  selected={values.pickupPointId === point.id}
                  onClick={(e) =>
                    setValues({
                      ...values,
                      pickupPointId: point.id,
                    })
                  }
                >
                  {point.name}
                </PickupPoint>
              </Col>
            );
          })}
        </Row>
        <Row style={{ marginBottom: "30px" }}>
          <Col>
            <FormItem>
              <label>Количество гостей:</label>
              <QuantityPicker
                value={values.guestCount}
                onChange={(value: number) =>
                  setValues({
                    ...values,
                    guestCount: value,
                  })
                }
              />
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col>
            <FormItem>
              <label>Комментарий к заказу:</label>
              <input
                type="text"
                placeholder=""
                value={values.comment}
                onChange={(e) =>
                  setValues({
                    ...values,
                    comment: e.target.value,
                  })
                }
              />
            </FormItem>
          </Col>
        </Row>
      </Container>
    </Root>
  );
});

export default Pickup;
