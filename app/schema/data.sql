/* Syed Abidur Rahman <<<===>>> 15/09/2012 */
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

INSERT INTO `mtt_lists` (`id`, `uuid`, `ow`, `name`, `d_created`, `d_edited`, `sorting`, `published`, `taskview`) VALUES
  (1, '54663f2e-2e7b-49b7-b9a3-d3f269016b10', 0, 'Todo', 1347585569, 0, 0, 0, 0),
  (2, '638dc66d-ae8c-40dd-b448-3f598a1bb1e6', 1, 'ha', 1347585603, 1347585603, 0, 0, 0),
  (3, 'c4bb9b39-b4ca-4ef4-95b9-dd5269e50e2e', 2, 'tanjim', 1347585615, 1347585615, 0, 0, 0);

INSERT INTO `mtt_todolist` (`id`, `uuid`, `list_id`, `d_created`, `d_completed`, `d_edited`, `compl`, `title`, `note`,
  `prio`, `ow`, `tags`, `tags_ids`, `duedate`) VALUES
  (1, '27ca44c7-7fcc-4737-a555-62df5c35fb30', 3, 1347585626, 0, 1347585644, 0, 'tumi ki koro?', 'jani naa', 0, 1, '', '', NULL);

SET FOREIGN_KEY_CHECKS=1;