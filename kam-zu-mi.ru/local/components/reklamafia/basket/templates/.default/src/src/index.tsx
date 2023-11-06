import * as React from "react";
import * as ReactDOM from "react-dom";

import App from "./components/App";
import { Store, StoreContext } from "./helpers/store.ts";

import { IAppProps } from "./interfaces";

class RootApp {
  appProps: IAppProps;
  store: any;

  constructor(appProps: IAppProps) {
    if (appProps.basket.length < 1) {
      window.location.href = "/";
    }

    this.appProps = appProps;
    this.attachReact();
  }

  attachReact() {
    ReactDOM.render(
      <StoreContext.Provider value={new Store(this.appProps)}>
        <App />
      </StoreContext.Provider>,
      document.getElementById("basket-application")
    );
  }
}

window.Basket = RootApp;
export default {};
