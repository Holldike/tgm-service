/*
    Stores all sent message
*/
CREATE TABLE IF NOT EXISTS sent_message
(
    tgm_contact_id          INT(11)    NOT NULL,
    tgm_to_contact_id       INT(11)    NOT NULL,
    tgm_message_id          INT(11)    NOT NULL,
    sent_at                 DATETIME   NOT NULL,
    /*
        tgm_to_user_id doesn't need for primary key because all message of each tgm user
        have unique id regardless from dialog or chat
    */
    PRIMARY KEY (tgm_user_id, tgm_message_id)
);

CREATE TABLE IF NOT EXISTS  log
(
    log_id                  INT(11)    AUTO_INCREMENT,
    request_body            TEXT       NOT NULL,
    respons_body            TEXT       NOT NULL,
    sent_at                 DATETIME   NOT NULL,
    PRIMARY KEY (log_id)
)