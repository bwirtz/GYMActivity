
-- Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
-- adupuis@genymobile.com
-- http://www.genymobile.com
-- 
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
-- 
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
-- 
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the
-- Free Software Foundation, Inc.,
-- 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA

SET NAMES 'utf8';

/* Drop all tables before creating them */

START TRANSACTION;
DROP TABLE ActivityReports;
DROP TABLE ActivityReportStatus;
DROP TABLE DailyFees;
DROP TABLE AssignementFees;
DROP TABLE Assignements;
DROP TABLE Activities;
DROP TABLE Tasks;
DROP TABLE Projects;
DROP TABLE ProjectStatus;
DROP TABLE ProjectTypes;
DROP TABLE ProjectTaskRelations;
DROP TABLE Clients;
DROP TABLE ProfileManagementData;
DROP TABLE Profiles;
DROP TABLE RightsGroups;
DROP TABLE AccessLogs;
DROP TABLE Ideas;
DROP TABLE IdeaStatus;
DROP TABLE IdeaMessages;
DROP TABLES Notifications;
DROP TABLES ApiKeys;
COMMIT;