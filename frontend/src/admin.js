import React from "react";
import ReactDOM from "react-dom";
import List from "./components/List";

import "../styles/admin.scss";

const App = () => {
  return (
    <main>
      <List />
    </main>
  );
};

const personelListAdminEl = document.getElementById("personnel-list-admin");

if (personelListAdminEl) {
  ReactDOM.render(<App />, personelListAdminEl);
}
