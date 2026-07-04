#!/bin/bash

# ===============================================
# Nexus School - Academic Domain Structure
# ===============================================

BASE="docs/02_Domain/Academic"

# Couleurs
GREEN='\033[0;32m'
NC='\033[0m' # No Color

echo ""
echo -e "${GREEN}=============================================${NC}"
echo -e "${GREEN} Creating Academic Domain Structure${NC}"
echo -e "${GREEN}=============================================${NC}"
echo ""

# Créer les dossiers
echo "Creating directories..."
mkdir -p "$BASE"/{Models,Relationships,Policies,Workflows,StateMachines,ValidationRules,Enums,DTOs,Services,Events,Permissions,UseCases}

# Models
for model in AcademicYear Term Program Level Series Classroom ClassSection Subject SubjectCoefficient Enrollment; do
    touch "$BASE/Models/$model.md"
done

# Relationships
for model in AcademicYear Term Program Level Series Classroom ClassSection Subject SubjectCoefficient Enrollment; do
    touch "$BASE/Relationships/$model.md"
done

# Policies
for model in AcademicYear Term Program Level Series Classroom ClassSection Subject SubjectCoefficient Enrollment; do
    touch "$BASE/Policies/$model.md"
done

# Workflows
for workflow in AcademicYearLifecycle StudentEnrollment StudentPromotion StudentTransfer StudentGraduation StudentWithdrawal SubjectAssignment SubjectCoefficientManagement ClassroomAssignment ClassSectionAssignment; do
    touch "$BASE/Workflows/$workflow.md"
done

# State Machines
for state in AcademicYear Enrollment Subject Classroom; do
    touch "$BASE/StateMachines/$state.md"
done

# Validation Rules
for validation in AcademicYear Term Program Level Series Classroom ClassSection Subject SubjectCoefficient Enrollment; do
    touch "$BASE/ValidationRules/$validation.md"
done

# Enums
for enum in AcademicYearStatus TermStatus EnrollmentStatus ProgramType SubjectType PromotionStatus; do
    touch "$BASE/Enums/$enum.md"
done

# DTOs
for dto in CreateAcademicYear UpdateAcademicYear CreateEnrollment UpdateEnrollment CreateSubject UpdateSubject; do
    touch "$BASE/DTOs/$dto.md"
done

# Services
for service in AcademicYearService EnrollmentService PromotionService ClassroomService SubjectService; do
    touch "$BASE/Services/$service.md"
done

# Events
for event in AcademicYearCreated EnrollmentCreated StudentPromoted StudentTransferred SubjectCreated; do
    touch "$BASE/Events/$event.md"
done

# Permissions
for permission in AcademicPermissions EnrollmentPermissions SubjectPermissions; do
    touch "$BASE/Permissions/$permission.md"
done

# Use Cases
for usecase in CreateAcademicYear CloseAcademicYear EnrollStudent PromoteStudent TransferStudent AssignSubject AssignCoefficient; do
    touch "$BASE/UseCases/$usecase.md"
done

echo ""
echo -e "${GREEN}=============================================${NC}"
echo -e "${GREEN} Academic Domain Structure Created Successfully${NC}"
echo -e "${GREEN}=============================================${NC}"
echo ""