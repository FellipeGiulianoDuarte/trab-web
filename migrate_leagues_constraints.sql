-- Migration script to update leagues table constraints
-- Run this script if you already have the database created

USE game_platform;

-- Drop the unique constraint on keyword if it exists
ALTER TABLE leagues DROP INDEX keyword;

-- Add unique constraint on name if it doesn't exist
ALTER TABLE leagues ADD UNIQUE INDEX unique_league_name (name);

-- Verify the changes
SHOW INDEX FROM leagues;
