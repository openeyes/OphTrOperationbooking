<?php

class m150424_112812_cancel_dead_patients_bookings extends CDbMigration
{

	public function up()
	{
		$storedProcedure = <<<EOL
CREATE PROCEDURE cancel_patient_bookings(IN patientToCancel INT)
  BEGIN
    DECLARE cancel_status, cancel_reason, booking_type, event_id, episode_id, booking_id, operation_id, episode_status_id, done, rows_affected, admin_user INT DEFAULT 0;
    DECLARE cancel_comment VARCHAR(255);
    DECLARE cur1 CURSOR FOR SELECT
                              event.id                                   AS event_id,
                              episode.id                                 AS episode_id,
                              ophtroperationbooking_operation_booking.id AS booking_id,
                              et_ophtroperationbooking_operation.id      AS operation_id
                            FROM episode
                              JOIN event ON episode.id = event.episode_id
                              JOIN et_ophtroperationbooking_operation
                                ON event.id = et_ophtroperationbooking_operation.event_id
                              LEFT JOIN ophtroperationbooking_operation_booking ON et_ophtroperationbooking_operation.id =
                                                                              ophtroperationbooking_operation_booking.element_id
                              LEFT JOIN ophtroperationbooking_operation_session
                                ON ophtroperationbooking_operation_session.id =
                                   ophtroperationbooking_operation_booking.session_id
                            WHERE episode.patient_id = patientToCancel
                                  AND event.event_type_id = @booking_type
                                  AND concat_ws(' ', ophtroperationbooking_operation_session.date,
                                                ophtroperationbooking_operation_session.start_time) > NOW() ||
                                                ophtroperationbooking_operation_session.date IS NULL;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    SET @cancel_comment = 'Automatically cancelled by system';

    SELECT id
    INTO @cancel_status
    FROM ophtroperationbooking_operation_status
    WHERE `name` = 'Cancelled';

    SELECT id
    INTO @cancel_reason
    FROM ophtroperationbooking_operation_cancellation_reason
    WHERE `text` = 'Patient has died';

    SELECT id
    INTO @booking_type
    FROM event_type
    WHERE `name` = 'Operation booking';

    SELECT id
    INTO @episode_status_id
    FROM episode_status
    WHERE `name` = 'Discharged';

    SELECT id
    INTO @admin_user
    FROM user
    WHERE `username` = 'admin';

    SELECT id
    INTO @audit_action_id
    FROM audit_action
    WHERE `name` = 'cancel';

    SELECT id
    INTO @audit_type_id
    FROM audit_type
    WHERE `name` = 'booking';

    OPEN cur1;

    read_loop: LOOP
      FETCH cur1
      INTO event_id, episode_id, booking_id, operation_id;
      IF done
      THEN
        LEAVE read_loop;
      END IF;
      UPDATE et_ophtroperationbooking_operation
      SET operation_cancellation_date = NOW(),
        cancellation_reason_id        = @cancel_reason,
        cancellation_comment          = @cancel_comment,
        cancellation_user_id          = @admin_user,
        status_id                     = @cancel_status
      WHERE id = operation_id
            AND cancellation_reason_id IS NULL;

      UPDATE episode
      SET episode_status_id = @episode_status_id
      WHERE id = episode_id;

      UPDATE ophtroperationbooking_operation_booking
      SET booking_cancellation_date = NOW(),
        cancellation_reason_id      = @cancel_reason,
        cancellation_comment        = @cancel_comment,
        cancellation_user_id        = @admin_user
      WHERE id = booking_id
            AND cancellation_reason_id IS NULL;

      INSERT INTO audit (action_id, type_id, patient_id, episode_id, event_id) VALUES (@audit_action_id, @audit_type_id, patientToCancel, episode_id, event_id);

    END LOOP;

    CLOSE cur1;
  END;

EOL;

		$trigger = <<<EOL
CREATE TRIGGER cancel_dead_patient_bookings AFTER UPDATE ON patient
FOR EACH ROW
  BEGIN
    IF NEW.date_of_death IS NOT NULL
    THEN
      CALL cancel_patient_bookings(NEW.id);
    END IF;
  END;
EOL;
		$this->execute($storedProcedure);
		$this->execute($trigger);
	}

	public function down()
	{
		$this->execute("DROP PROCEDURE IF EXISTS cancel_patient_bookings;");
		$this->execute("DROP TRIGGER cancel_dead_patient_bookings;");
	}

}