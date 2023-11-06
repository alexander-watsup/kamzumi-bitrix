import * as React from "react";
import { useState, useEffect, useContext } from "react";

import { observer } from "mobx-react-lite";
import { StoreContext } from "../../../../helpers/store";

import styled from "styled-components";
import { Container, Row, Col } from "react-awesome-styled-grid";

import { titleCase, layoutFix } from "../../../../helpers/helpers";

import InputMask from "react-input-mask";
import * as Autocomplete from "react-autocomplete";
import QuantityPicker from "./../QuantityPicker";

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

  if (values.city.id.length == 0) result.city = "";
  if (values.street.id.length == 0) result.street = "";
  if (values.house.length == 0) result.house = "";
  // отключена обязательность поля "Квартира"
  // if (values.apartment.length == 0) result.apartment = "";

  return result;
};

const CourierDeliveryForm = observer(() => {
  const store = useContext(StoreContext);
  const [values, setValues] = useState({
    name: "",
    phone: {
      value: "",
      valueFiltered: "",
    },
    city: {
      id: store.initValues.deliveryCities[0].id,
      name: store.initValues.deliveryCities[0].name,
    },
    street: {
      value: "",
      id: "",
      name: "",
    },
    house: "",
    housing: "",
    apartment: "",
    guestCount: 1,
    comment: "",
  });

  const errors = validate(values);
  const isValid = Object.keys(errors).length === 0;

  const [streets, setStreets] = useState([]);
  useEffect(() => {
    if (values.city.id === "") return;
    let url =
      "https://kam-zu-mi.ru/api/v1/iiko/city/" + values.city.id + "/streets";
    fetch(url)
      .then((res) => res.json())
      .then((res) => {
        setStreets(res.data);
      });
  }, [values.city.id]);

  // Подготовка диагностического сообщения для Телеграмм
  useEffect(() => {
    let data =
      values.name +
      " " +
      values.phone.value +
      "\r\n" +
      values.city.name +
      " " +
      (values.street.name ? " ул." + values.street.name : "") +
      (values.house ? " д." + values.house : "") +
      (values.housing ? " корп." + values.housing : "") +
      (values.apartment ? " кв." + values.apartment : "") +
      (values.guestCount ? " гостей:" + values.guestCount : "") +
      (values.comment ? " коммент." + values.comment : "");

    data += "\r\n";
    store.initValues.basket.map((item) => {
      data += " " + item.name + " " + item.quantity + "шт.";
      //return { id: item.id, amount: item.quantity };
    });
    store.diagUserFormData = data;
    store.diagUserPhone = values.phone.value;
  }, [values]);

  useEffect(() => {
    if (!isValid) {
      store.prepareCourierDelivery(null);
    } else {
      store.prepareCourierDelivery({
        name: values.name,
        phone: values.phone.valueFiltered,
        comment: values.comment,
        guestCount: values.guestCount,
        courier: {
          city: values.city.name,
          street: values.street.name,
          house: values.house,
          housing: values.housing,
          apartment: values.apartment,
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
                className={errors.name ? "error" : ""}
                placeholder=""
                name="name"
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
        <Row>
          <Col xs={4} sm={4}>
            <FormItem>
              <label>Город:</label>
              <select
                onChange={(e) => {
                  setValues({
                    ...values,
                    city: {
                      name: e.target.selectedOptions[0].text,
                      id: e.target.value,
                    },
                    street: {
                      value: "",
                      name: "",
                      id: "",
                    },
                  });
                }}
                value={values.city.id}
              >
                {store.initValues.deliveryCities.map(
                  (city: { id: string; name: string }) => {
                    return (
                      <option value={city.id} key={city.id}>
                        {city.name}
                      </option>
                    );
                  }
                )}
              </select>
            </FormItem>
          </Col>
          <Col xs={4} sm={4}>
            <FormItem>
              <label>
                Улица: <span>*</span>
              </label>
              <Autocomplete
                getItemValue={(item) => item.id}
                open={values.street.value.length > 2 && values.street.id === ""}
                items={streets}
                shouldItemRender={(item, value) =>
                  item.name.toLowerCase().includes(value.toLowerCase())
                }
                renderItem={(item, isHighlighted) => (
                  <div
                    style={{
                      background: isHighlighted ? "#f8ca4d" : "white",
                      fontSize: "2em",
                      cursor: "pointer",
                    }}
                    key={item.id}
                  >
                    {item.name}
                  </div>
                )}
                value={values.street.value}
                onChange={(e) => {
                  let newValue = layoutFix(e.target.value).replace(
                    /[^А-Яа-яЁё\s\d]/g,
                    ""
                  );
                  setValues({
                    ...values,
                    street: {
                      value: newValue,
                      id: "",
                      name: "",
                    },
                  });
                }}
                onSelect={(e, item) => {
                  setValues({
                    ...values,
                    street: {
                      value: item.name,
                      id: item.id,
                      name: item.name,
                    },
                  });
                }}
                inputProps={{
                  onBlur: () => {
                    if (values.street.id === "") {
                      setValues({
                        ...values,
                        street: {
                          value: "",
                          id: "",
                          name: "",
                        },
                      });
                    }
                  },
                }}
              />
            </FormItem>
          </Col>
        </Row>
        <Row style={{ marginBottom: "30px" }}>
          <Col>
            <FormItem>
              <label>
                Дом: <span>*</span>
              </label>
              <input
                type="text"
                placeholder=""
                disabled={values.street.name === ""}
                onChange={(e) =>
                  setValues({
                    ...values,
                    house: e.target.value,
                  })
                }
                value={values.street.name === "" ? "" : values.house}
                maxLength={5}
              />
            </FormItem>
          </Col>
          <Col>
            <FormItem>
              <label>Корпус:</label>
              <input
                type="text"
                placeholder=""
                value={values.street.name === "" ? "" : values.housing}
                disabled={values.street.name === ""}
                maxLength={5}
                onChange={(e) =>
                  setValues({
                    ...values,
                    housing: e.target.value,
                  })
                }
              />
            </FormItem>
          </Col>
          <Col>
            <FormItem>
              <label>
                Квартира \ Офис:
              </label>
              <input
                type="text"
                placeholder=""
                value={values.street.name === "" ? "" : values.apartment}
                disabled={values.street.name === ""}
                maxLength={5}
                onChange={(e) =>
                  setValues({
                    ...values,
                    apartment: e.target.value,
                  })
                }
              />
            </FormItem>
          </Col>
        </Row>
        <Row>
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

export default CourierDeliveryForm;
