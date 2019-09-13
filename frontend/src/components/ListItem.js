import React, { useState, useEffect } from "react";

const PersonList = props => {
  const { person, deletePerson, editPerson } = props;
  const [submitting, setSubmitting] = useState(false);

  // Unmount
  useEffect(() => {
    return () => setSubmitting(false);
  }, []);

  const handleDeletion = async person => {
    if (confirm(`${person.name} will be removed from the list`)) {
      setSubmitting(true);
      await deletePerson(person);
    }
  };

  return (
    <li className="personnelList_item">
      <img src="https://via.placeholder.com/50" alt={person.name} />

      <div className="personnelList_item_info">
        <h2 className="personnelList_item_title">
          {person.name} -{" "}
          <span className="personnelList_item_position">{person.position}</span>
        </h2>
        <p className="personnelList_item_description">
          {person.short_description}
        </p>
      </div>

      <div className="personnelList_item_actions">
        <button
          type="button"
          className="button button-secondary"
          onClick={() => editPerson(person)}
        >
          Edit
        </button>
        <button
          type="button"
          className="button button-secondary button-link-delete"
          disabled={submitting}
          onClick={() => {
            handleDeletion(person);
          }}
        >
          Delete
        </button>
      </div>
    </li>
  );
};

export default PersonList;
