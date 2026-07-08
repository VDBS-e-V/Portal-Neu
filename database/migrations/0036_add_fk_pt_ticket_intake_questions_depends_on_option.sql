ALTER TABLE `pt_ticket_intake_questions`
  ADD CONSTRAINT `fk_pt_ticket_intake_questions_depends_on_option`
    FOREIGN KEY (`depends_on_option_id`) REFERENCES `pt_ticket_intake_question_options` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;
