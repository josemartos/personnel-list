import React, { useState, useEffect } from "react";
import Api from "../utils/Api";
import Spinner from "./Spinner";
import ListItem from "./ListItem";
import Form from "./Form";

const PersonList = () => {
  const [loading, setLoading] = useState(true);
  const [formVisible, setFormVisible] = useState(false);
  const [persons, setPersons] = useState([]);
  const [person, setPerson] = useState({});

  useEffect(() => {
    Api.get("/persons").then(response => {
      setPersons(response.data || []);
      setLoading(false);
    });
  }, []);

  useEffect(() => {
    setPerson({});
  }, [persons]);

  const removeFromList = itemId =>
    persons.filter(person => person.id != itemId);

  const createPerson = result => {
    return Api.post("/persons", result)
      .then(response => {
        const updatedPersons = [...persons, ...[response.data]];
        setPersons(updatedPersons);
      })
      .catch(() => {
        // Handle error!
      });
  };

  const updatePerson = result => {
    return Api.put(`/persons/${result.id}`, result)
      .then(response => {
        const updatedPersons = removeFromList(response.data.id);
        setPersons([...updatedPersons, ...[response.data]]);
      })
      .catch(() => {
        // Handle error!
      });
  };

  const deletePerson = person => {
    return Api.delete(`/persons/${person.id}`)
      .then(response => {
        const updatedPersons = removeFromList(response.data.id);
        setPersons(updatedPersons);
      })
      .catch(() => {
        // Handle error!
      });
  };

  const editPerson = personInfo => {
    setPerson({ ...personInfo });
    setFormVisible(true);
    window.scrollTo(0, 0);
  };

  const handleFormVisible = () => {
    setFormVisible(!formVisible);
  };

  const handleFormSubmit = result => {
    if (person && person.id) {
      // Merges the existing id with the new input
      result = { ...person, ...result };
      return updatePerson(result);
    }

    return createPerson(result);
  };

  return (
    <div className="personnelList_admin">
      <h1 className="pageTitle">
        Personnel List{" "}
        <button
          type="button"
          className="page-title-action"
          onClick={handleFormVisible}
        >
          Add New
        </button>
      </h1>
      {formVisible ? (
        <Form
          submitText="Apply"
          onSubmit={handleFormSubmit}
          onClose={handleFormVisible}
          model={person}
          fields={[
            {
              key: "name",
              placeholder: "*Name",
              type: "input",
              inputType: "text",
              required: true
            },
            {
              key: "position",
              placeholder: "*Position",
              type: "input",
              inputType: "text",
              required: true
            },
            {
              key: "short_description",
              placeholder: "*Short description (max: 150)",
              type: "textarea",
              maxLength: 150,
              required: true
            },
            {
              key: "github",
              placeholder: "Github",
              type: "input",
              inputType: "text"
            },
            {
              key: "linkedin",
              placeholder: "Linkedin",
              type: "input",
              inputType: "text"
            },
            {
              key: "xing",
              placeholder: "Xing",
              type: "input",
              inputType: "text"
            },
            {
              key: "facebook",
              placeholder: "Facebook",
              type: "input",
              inputType: "text"
            }
          ]}
        />
      ) : (
        ""
      )}
      <ul>
        {!persons.length ? (
          <li>
            {loading ? <Spinner /> : "No company members, please create one"}
          </li>
        ) : (
          persons.map(person => (
            <ListItem
              key={person.id}
              person={person}
              deletePerson={deletePerson}
              editPerson={editPerson}
            />
          ))
        )}
      </ul>
    </div>
  );
};

export default PersonList;
