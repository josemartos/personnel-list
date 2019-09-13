import React from "react";
import { SelectControl } from "@wordpress/components";
const { registerBlockType } = window.wp.blocks;
const { withSelect } = window.wp.data;

import "../styles/block.scss";

const PersonSelect = ({ personsList, props }) => {
  if (!personsList) {
    return "Loading...";
  }

  const {
    attributes: { persons },
    setAttributes
  } = props;
  const selectOptions = personsList.map(person => ({
    value: person.id,
    label: person.title.rendered
  }));

  const onChangeMembers = person => {
    // If multiple is true the value received is an array of the selected value
    const selectedPerson = person[0];
    let updateMembers;

    if (persons.includes(selectedPerson)) {
      // removes the selection
      updateMembers = persons.filter(m => m !== selectedPerson);
    } else {
      // adds the selection
      updateMembers = [...persons, ...person];
    }

    setAttributes({ persons: updateMembers });
  };

  return (
    <SelectControl
      tagName="select"
      multiple
      label={"Select list of persons to show:"}
      value={persons}
      onChange={onChangeMembers}
      options={selectOptions}
    />
  );
};

registerBlockType("personnel-list/add-person", {
  title: "Personnel List",
  icon: "businessman", // Dashicons
  category: "common", // common, formatting, layout, widgets, embed
  attributes: {
    persons: {
      type: "array",
      default: []
    }
  },
  edit: withSelect((select, props) => ({
    personsList: select("core").getEntityRecords("postType", "person"),
    props
  }))(PersonSelect),
  save: () => null
});
