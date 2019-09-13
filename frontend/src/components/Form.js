import React, { useState, useEffect } from "react";

export const FieldTypes = {
  INPUT: "input",
  TEXTAREA: "textarea"
};

const Form = props => {
  const [values, setValues] = useState({});
  const [submitting, setSubmitting] = useState(false);

  // init state
  useEffect(() => {
    const newValues = {};
    props.fields.forEach(field => {
      newValues[field.key] = props.model[field.key] || "";
    });
    setValues(newValues);
  }, [props.model, props.fields]);

  const onSubmit = e => {
    e.preventDefault();

    setSubmitting(true);
    props
      .onSubmit(values)
      .then(() => clearForm())
      .catch(() => {
        // Handle error!
      })
      .finally(() => {
        setSubmitting(false);
      });
  };

  const onChange = (e, key) => {
    setValues({
      ...values,
      ...{
        [key]: e.currentTarget.value
      }
    });
  };

  const clearForm = () => {
    props.fields.forEach(field => [setValues({ [field.key]: "" })]);
  };

  const closeForm = () => {
    clearForm();
    props.onClose();
  };

  const renderField = field => {
    switch (field.type) {
      case FieldTypes.INPUT:
      default:
        return [
          <input
            key={"input"}
            type={field.inputType}
            name={field.key}
            value={values[field.key] || ""}
            placeholder={field.placeholder}
            className="form_field"
            onChange={e => onChange(e, field.key)}
            required={field.required || false}
          />
        ];
      case FieldTypes.TEXTAREA:
        return [
          <textarea
            key={"textarea"}
            name={field.key}
            value={values[field.key] || ""}
            placeholder={field.placeholder}
            maxLength={field.maxLength}
            className="form_field form_textarea"
            onChange={e => onChange(e, field.key)}
            required={field.required || false}
          />
        ];
    }
  };

  const renderFormFields = () =>
    props.fields.map(field => [
      field.label ? <label htmlFor={field.key}>{field.label}</label> : null,
      renderField(field)
    ]);

  return (
    <form className="form" onSubmit={onSubmit}>
      <div className="form_fields">{renderFormFields()}</div>
      <div className="form_actions">
        <button
          className="button button-primary"
          type="submit"
          disabled={submitting}
        >
          {props.submitText}
        </button>
        <button
          className="button button-secondary"
          type="button"
          onClick={clearForm}
          disabled={submitting}
        >
          Clear
        </button>
        <button
          className="button button-secondary"
          type="button"
          onClick={closeForm}
          disabled={submitting}
        >
          Close
        </button>
      </div>
    </form>
  );
};

export default Form;
