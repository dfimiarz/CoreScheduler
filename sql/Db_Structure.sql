CREATE VIEW core_event_details_view AS SELECT cta.id as record_id,cu.id user_id,cu.firstname,cu.lastname,cu.username,cu.email,concat(p.first_name,' ',p.last_name) as piname,cta.time_modified as timestamp,cta.start,cta.end,cta.note,cta.state as event_state,cta.service_id,cs.short_name as service_name,cr.name as resource_name FROM core_timed_activity cta, core_users cu,core_services cs,core_resources cr, people p WHERE cu.id = cta.user AND cs.id = cta.service_id AND p.individual_id = cu.pi AND cr.id = cs.resource_id;

CREATE TABLE IF NOT EXISTS `scidiv`.`core_perm_abac` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `perm_id` INT NOT NULL,
  `attribs` TEXT NOT NULL,
  INDEX `tpconf_perm_id_idx` (`perm_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `tpconf_perm_id`
    FOREIGN KEY (`perm_id`)
    REFERENCES `scidiv`.`core_permission` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Implementation of attribute based access controll system'