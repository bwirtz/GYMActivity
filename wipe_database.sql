
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
DROP VIEW ActivityReportRessources;
DROP VIEW ActivityReportWorkflow;
DROP TABLE ActivityReports;
DROP TABLE ActivityReportStatus;
DROP TABLE DailyRates;
DROP TABLE AssignementFees;
DROP TABLE Assignements;
DROP TABLE Activities;
DROP TABLE BankHolidays;
DROP TABLE Ideas;
DROP TABLE IdeaStatus;
DROP TABLE IdeaMessages;
DROP TABLE IdeaVotes;
DROP TABLE Notifications;
DROP TABLE ApiKeys;
DROP TABLE PropertyTypes;
DROP TABLE Properties;
DROP TABLE PropertyOptions;
DROP TABLE PropertyValues;
DROP TABLE CareerEvents;
DROP TABLE HolidaySummaries;
DROP TABLE HolidaySummariesArchived;
DROP TABLE HolidaySummaries_NG;
DROP TABLE IntranetHistories;
DROP TABLE IntranetTagPageRelations;
DROP TABLE IntranetPages;
DROP TABLE IntranetTags;
DROP TABLE IntranetTypes;
DROP TABLE IntranetCategories;
DROP TABLE IntranetPageStatus;
DROP TABLE Tasks;
DROP TABLE Projects;
DROP TABLE ProjectStatus;
DROP TABLE ProjectTypes;
DROP TABLE ProjectTaskRelations;
DROP TABLE Clients;
DROP TABLE Countries;
DROP TABLE ProfileManagementData;
DROP TABLE Profiles;
DROP TABLE RightsGroups;
DROP TABLE AccessLogs;
COMMIT;
